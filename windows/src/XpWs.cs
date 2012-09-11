using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class XpWs : BaseRunner
    {

        static void Main(string[] args)
        {
            string server, port;

            var pid  = System.Diagnostics.Process.GetCurrentProcess().Id;
            var addr = new string[] { "localhost" };
            var profile = "dev";
            var i = 0;
            var parsing = true;

            // Parse arguments
            while (parsing && i < args.Length)
            {
                switch (args[i])
                {
                    case "-p":
                        profile = args[++i];
                        break;

                    default: 
                        addr = args[i].Split(':');
                        parsing = false;
                        break;
                }
                i++;
            }

            server = addr[0];
            port = addr.Length > 1 ? addr[1] : "8080";
            if (i > 0)
            {
                Array.Copy(args, i, args, 0, args.Length - i);
                Array.Resize(ref args, args.Length - i);
            }

            // Execute
            var proc = Executor.Instance(Paths.DirName(Paths.Binary()), "web", "", new string[] { "." }, args);
            proc.StartInfo.Arguments = "-S " + server + ":" + port + " " + proc.StartInfo.Arguments;
            try
            {
                Environment.SetEnvironmentVariable("SERVER_PROFILE", profile);

                proc.Start();
                Console.Out.WriteLine("[xpws-{0}#{1}] running @ {2}:{3}. Press <Enter> to exit", profile, pid, server, port);
                Console.Read();
                Console.Out.WriteLine("[xpws-{0}#{1}] shutting down...", profile, pid);
                proc.Kill();
                proc.WaitForExit();
                Environment.Exit(proc.ExitCode);
            }
            catch (SystemException e) 
            {
                Console.Error.WriteLine("*** " + proc.StartInfo.FileName + ": " + e.Message);
                Environment.Exit(0xFF);
            }
            finally
            {
                proc.Close();
            }
        }
    }
}
