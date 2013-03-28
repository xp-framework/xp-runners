using System;
using System.IO;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class XpWs : BaseRunner
    {
        delegate int Execution(string profile, string server, string port, string web, string root, string config, string[] inc);

        /// Delegate: Serve web
        static int Serve(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            var pid = System.Diagnostics.Process.GetCurrentProcess().Id;

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

            try
            {
                Environment.SetEnvironmentVariable("WEB_ROOT", web);
                Environment.SetEnvironmentVariable("SERVER_PROFILE", profile);
                Environment.SetEnvironmentVariable("DOCUMENT_ROOT", root);

                proc.Start();
                Console.Out.WriteLine("[xpws-{0}#{1}] running {2}:{3} @ {4} - Press <Enter> to exit", profile, pid, server, port, web);
                Console.Read();
                Console.Out.WriteLine("[xpws-{0}#{1}] shutting down...", profile, pid);
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
