using System;
using System.IO;
using System.Collections.Generic;
using System.Threading;
using System.Threading.Tasks;
using System.Diagnostics;

namespace Net.XpFramework.Runner
{
    class XpWs : BaseRunner
    {
        delegate int Execution(string profile, string server, string port, string web, string root, string config, string[] inc);

        static int Service(string profile, string server, string port, string web, string root, string config, Func<Process> NewProcess)
        {
            var pid = Process.GetCurrentProcess().Id;

            if ("-" == config) 
            {
                Console.WriteLine("No configuration given, serving static content from {0}", root);
            }

            // Execute
            var proc = NewProcess();
            try
            {
                Environment.SetEnvironmentVariable("WEB_ROOT", web);
                Environment.SetEnvironmentVariable("SERVER_PROFILE", profile);
                Environment.SetEnvironmentVariable("DOCUMENT_ROOT", root);

                proc.Start();
                Console.Out.WriteLine("[xpws-{0}#{1}] running {2}:{3} @ {4} - Press <Enter> to exit", profile, pid, server, port, web);

                // Route output through this command in XP 6+. This way, we prevent 
                // PHP garbling the output on a Windows console window.
                if (proc.StartInfo.RedirectStandardOutput)
                {
                    Task.Factory.StartNew(() => { Executor.Process(proc.StandardOutput, Console.Out); });
                    Task.Factory.StartNew(() => { Executor.Process(proc.StandardError, Console.Error); });
                }

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

        /// Delegate: Serve web with development webserver
        static int Develop(string profile, string server, string port, string web, string root, string config, string[] inc)
        {
            return Service(profile, server, port, web, root, config, () => {
                var proc = Executor.Instance(Paths.DirName(Paths.Binary()), "web", "", inc, new string[] { });
                proc.StartInfo.Arguments = (
                    "-S " + server + ":" + port +
                    " -t \"" + root + "\"" +
                    " -duser_dir=\"" + config + "\" " +
                    proc.StartInfo.Arguments
                );
                return proc;
            });
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
            Execution action = Develop;
            var addr = new string[] { "localhost" };
            var inc = new List<string>(new string[] { "." });
            var web = ".";
            var root = "";
            var config = "";
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

                    case "-m":
                        var mode = args[++i];
                        if ("develop" == mode)
                        {
                            action = Develop;
                        }
                        else
                        {
                            action = (_profile, _server, _port, _web, _root, _config, _inc) =>
                            {
                                return Service(_profile, _server, _port, _web, _root, _config, () => {
                                    return Executor.Instance(Paths.DirName(Paths.Binary()), "class", "xp.scriptlet.Server", _inc, new string[] {
                                        _web,
                                        _config,
                                        _profile,
                                        _server + ":" + _port,
                                        mode
                                    });
                                });
                            };
                        }
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

            // Verify we have an application to run or a config to read it from
            var dir = String.IsNullOrEmpty(config) ? "etc" : config;
            if (File.Exists(Paths.Compose(dir, "web.ini")))
            {
                config = Paths.Resolve(dir);
            }
            else if ("" == config || "-" == config)
            {
                config = "-";
            }
            else
            {
                config = ":" + config;
            }

            // If no document root has been supplied, check for an existing "static"
            // subdirectory inside the web root; otherwise just use the web root
            web = Paths.Resolve(web);
            if (String.IsNullOrEmpty(root))
            {
                var path = Paths.Compose(web, "static");
                root = Directory.Exists(path) ? path : web;
            }
            else
            {
                root = Paths.Resolve(root);
            }

            // Run
            Environment.Exit(action(
                profile,
                addr[0],
                addr.Length > 1 ? addr[1] : "8080",
                web,
                root,
                config,
                inc.ToArray()
            ));
        }
    }
}
