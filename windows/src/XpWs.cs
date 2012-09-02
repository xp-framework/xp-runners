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

        	// Parse arguments
        	if (0 == args.Length) 
        	{
        		server = "localhost";
        		port = "8080";
        	} 
        	else 
        	{
        		var parsed = args[0].Split(':');
        		server = parsed[0];
        		port = parsed.Length > 1 ? parsed[1] : "8080";
        		Array.Copy(args, 1, args, 0, args.Length - 1);
        		Array.Resize(ref args, args.Length - 1);
        	}

            // Execute
            var proc = Executor.Instance(Paths.DirName(Paths.Binary()), "web", "", new string[] { "." }, args);
            proc.StartInfo.Arguments = "-S " + server + ":" + port + " " + proc.StartInfo.Arguments;
            try
            {
	            Environment.SetEnvironmentVariable("DOCUMENT_ROOT", "./0");
                proc.Start();
                Console.Out.WriteLine("[xpws#{0}] running @ {1}:{2}. Press <Enter> to exit", pid, server, port);
                Console.Read();
                Console.Out.WriteLine("[xpws#{0}] shutting down...", pid);
                proc.Kill();
                proc.WaitForExit();
                Environment.Exit(proc.ExitCode);
            }
            catch (SystemException e) 
            {
                Console.Error.WriteLine("*** " + proc.StartInfo.FileName + ": " + e.Message);
            }
            finally
            {
            	proc.Close();
            }
        }
    }
}
