using System;
using System.IO;
using System.Collections.Generic;
using System.Diagnostics;

namespace Net.XpFramework.Runner
{
    class XpWs : BaseRunner
    {
        delegate int Execution(string profile, string server, string port, string web, string root, string config, string[] inc);

        protected static Process NewProcess(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            // If no document root has been supplied, check for an existing "static"
            // subdirectory inside the web root; otherwise just use the web roor
            if (String.IsNullOrEmpty(root))
            {
                var path = Paths.Compose(Paths.Resolve(web), "static");
                root = Directory.Exists(path) ? path : web;
            }
            else
            {
                root = Paths.Resolve(root);
            }

            // Execute
            var proc = Executor.Instance(Paths.DirName(Paths.Binary()), "web", "", inc, new string[] { });
            proc.StartInfo.Arguments = (
                "-S " + server + ":" + port +
                " -t \"" + root + "\"" +
                " -duser_dir=\"" + config + "\" " +
                proc.StartInfo.Arguments
            );

            Environment.SetEnvironmentVariable("WEB_ROOT", web);
            Environment.SetEnvironmentVariable("SERVER_PROFILE", profile);
            Environment.SetEnvironmentVariable("DOCUMENT_ROOT", root);

            return proc;
        }

        /// Delegate: Serve web
        static int Serve(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            var proc = NewProcess(profile, server, port, web, root, config, inc);
            try
            {
                proc.Start();
                Console.Out.WriteLine("[xpws-{0}#{1}] running {2}:{3} @ {4} - Press <Enter> to exit", profile, proc.Id, server, port, web);
                Console.Read();
                Console.Out.WriteLine("[xpws-{0}#{1}] shutting down...", profile, proc.Id);
                proc.Kill();
                proc.WaitForExit();
                return proc.ExitCode;
            }
            catch (SystemException e)
            {
                Console.Error.WriteLine("*** " + proc.StartInfo.FileName + ": " + e.Message);
                return 0xFF;
            }
            finally
            {
                proc.Close();
            }
        }

        protected static string PidFile()
        {
            return Paths.Compose(Environment.GetFolderPath(Environment.SpecialFolder.Personal), ".xpws.pid");
        }

        /// Delegate: Start serving web
        static int Start(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            var proc = NewProcess(profile, server, port, web, root, config, inc);
            try
            {
                proc.Start();

                // Write PID file and exit
                File.WriteAllText(PidFile(), profile + '#' + proc.Id);
                Console.Out.WriteLine("[xpws-{0}#{1}] running {2}:{3} @ {4} - Use xpws stop to end", profile, proc.Id, server, port, web);
                return 0;
            }
            catch (SystemException e)
            {
                Console.Error.WriteLine("*** " + proc.StartInfo.FileName + ": " + e.Message);
                return 0xFF;
            }
            finally
            {
                proc.Close();
            }
        }

        /// Delegate: Stop serving web
        static int Status(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            var pidFile = PidFile();

            if (!File.Exists(pidFile))
            {
                Console.WriteLine("xpws not running");
                return 1;
            }
            else
            {
                Console.WriteLine("[xpws-{0}] running {1}", File.ReadAllText(pidFile).Trim(), pidFile);
                return 0;
            }
        }

        /// Delegate: Stop serving web
        static int Stop(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            var pidFile = PidFile();

            if (!File.Exists(pidFile))
            {
                Console.Error.WriteLine("*** xpws not running");
                return 0xFF;
            }

            // Parse pid file, then delete it
            var spec = File.ReadAllText(pidFile).Trim().Split('#');
            var running = spec[0];
            var pid = Convert.ToInt32(spec[1]);
            File.Delete(pidFile);

            // Close process
            Process proc = null;
            try
            {
                proc = Process.GetProcessById(pid);
            }
            catch (ArgumentException e)
            {
                Console.Error.WriteLine("*** Cannot shut down xpws: " + e.Message);
                return 0xFF;
            }

            try
            {
                Console.Out.WriteLine("[xpws-{0}#{1}] shutting down...", running, pid);
                proc.Kill();
                proc.WaitForExit();
                return 0;
            }
            finally
            {
                proc.Close();
            }
        }

        /// Delegate: Inspect web setup
        static int Inspect(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            Execute("class", "xp.scriptlet.Inspect", inc, new string[]
            {
                web,
                config,
                profile,
                server + ":" + port
            });
            return 0;
        }

        /// Entry point
        static void Main(string[] args)
        {
            Execution action = Serve;
            var addr = new string[] { "localhost" };
            var inc = new List<string>(new string[] { "." });
            var web = ".";
            var root = "";
            var config = "etc";
            var profile = "dev";

            // Parse arguments
            var i = 0;
            while (i < args.Length)
            {
                switch (args[i])
                {
                    case "-p":
                        profile = args[++i];
                        break;

                    case "-r":
                        root = args[++i];
                        if (!Directory.Exists(root))
                        {
                            Console.Error.WriteLine("*** Document root {0} does not exist, exiting.", root);
                            Environment.Exit(0x03);
                        }
                        break;

                    case "-w":
                        web = args[++i];
                        break;

                    case "-c":
                        config = args[++i];
                        break;

                    case "-cp":
                        inc.Add(Paths.Resolve(args[++i]));
                        break;

                    case "-i":
                        action = Inspect;
                        break;

                    case "-?":
                        Execute("class", "xp.scriptlet.Usage", inc.ToArray(), new string[] { "xpws.txt" });
                        return;

                    case "start":
                        action = Start;
                        break;

                    case "status":
                        action = Status;
                        break;

                    case "stop":
                        action = Stop;
                        break;

                    default:
                        addr = args[i].Split(':');
                        break;
                }
                i++;
            }

            // Verify we have a web.ini
            if (!File.Exists(Paths.Compose(config, "web.ini")))
            {
                Console.Error.WriteLine("*** Cannot find the web configuration web.ini in {0}/, exiting.", config);
                Environment.Exit(0x03);
            }

            // Run
            Environment.Exit(action(
                profile,
                addr[0],
                addr.Length > 1 ? addr[1] : "8080",
                Paths.Resolve(web),
                root,
                Paths.Resolve(config),
                inc.ToArray()
            ));
        }
    }
}
