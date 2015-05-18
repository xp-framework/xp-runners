using System;
using System.IO;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Unittest : BaseRunner
    {

        static void Main(string[] args)
        {
            var watch = Array.IndexOf(args, "-w");
            if (watch < 0)
            {
                Execute("class", "xp.unittest.Runner", new string[] { "." }, args);
            }
            else
            {
                var remaining = new string[args.Length - 2];
                Array.Copy(args, remaining, watch);
                Array.Copy(args, watch + 2, remaining, watch, args.Length - watch - 2);

                var watcher = new FileSystemWatcher {
                  Path = args[watch + 1],
                  IncludeSubdirectories = true,
                  Filter = "*.*"
                };

                using (watcher) 
                {
                    watcher.EnableRaisingEvents = true;

                    do
                    {
                        try
                        {
                            Executor.Execute(Paths.DirName(Paths.Binary()), "class", "xp.unittest.Runner", new string[] { "." }, remaining);
                        }
                        catch (Exception e) 
                        {
                            Console.Error.WriteLine("*** " + e.GetType() + ": " + e.Message);
                            Environment.Exit(0xFF);
                        }

                        watcher.WaitForChanged(WatcherChangeTypes.Changed);
                    } while (true);
                }
                Environment.Exit(0);
            }
        }
    }
}
