using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class XpWs : BaseRunner
    {
        delegate int Execution(string profile, string server, string port, string root);

        /// Delegate: Serve web
        static int Serve(string profile, string server, string port, string root)
        {
            var pid = System.Diagnostics.Process.GetCurrentProcess().Id;
            var args = "-S " + server + ":" + port;

            if (!String.IsNullOrEmpty(root))
            {
                Environment.SetEnvironmentVariable("DOCUMENT_ROOT", root);
                args += " -t " + root;
            }

            // Execute
            var proc = Executor.Instance(Paths.DirName(Paths.Binary()), "web", "", new string[] { "." }, new string[] { });
            proc.StartInfo.Arguments = args + " " + proc.StartInfo.Arguments;
            try
            {
                Environment.SetEnvironmentVariable("SERVER_PROFILE", profile);

                proc.Start();
                Console.Out.WriteLine("[xpws-{0}#{1}] running @ {2}:{3}. Press <Enter> to exit", profile, pid, server, port);
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
        static int Inspect(string profile, string server, string port, string root)
        {
            Execute("class", "xp.scriptlet.Inspect", new string[] { "." }, new string[] {
                ".",
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
            var profile = "dev";
            var root = "";

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
                        break;

                    case "-i":
                        action = Inspect;
                        break;

                    case "-?":
                        Execute("class", "xp.scriptlet.Usage", new string[] { "." }, new string[] { "xpws.txt" });
                        return;

                    default:
                        addr = args[i].Split(':');
                        break;
                }
                i++;
            }

            // Verify we have a web.ini
            if (!System.IO.File.Exists(Paths.Compose("etc", "web.ini")))
            {
                Console.Error.WriteLine("*** Cannot find the web configuration web.ini in etc/, exiting.");
                Environment.Exit(0x03);
            }

            // Run
            Environment.Exit(action(profile, addr[0], addr.Length > 1 ? addr[1] : "8080", root));
        }
    }
}
