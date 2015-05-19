using System;
using System.IO;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Unittest : BaseRunner
    {

        /// Runs the command and returns the exitcode
        private static int Run(string[] args)
        {
            try
            {
                return Executor.Execute(Paths.DirName(Paths.Binary()), "class", "xp.unittest.Runner", new string[] { "." }, args);
            }
            catch (Exception e) 
            {
                Console.Error.WriteLine("*** " + e.GetType() + ": " + e.Message);
                return -1;
            }
        }

        static void Main(string[] args)
        {
            var watch = Array.IndexOf(args, "-w");
            if (watch < 0)
            {
                Environment.Exit(Run(args));
            }
            else
            {
                // First run may not exit with a startup or argument error
                var code = Run(args);
                if (-1 == code)
                {
                    Environment.Exit(0xFF);
                }
                else if (2 == code)
                {
                    Environment.Exit(2);
                }

                var watcher = new FileSystemWatcher {
                  Path = args[watch + 1],
                  IncludeSubdirectories = true,
                  Filter = "*.*"
                };
                using (watcher) 
                {
                    watcher.EnableRaisingEvents = true;
                    while (!watcher.WaitForChanged(WatcherChangeTypes.Changed).TimedOut)
                    {
                        Run(args);
                    }
                }
                Environment.Exit(0);
            }
        }
    }
}
