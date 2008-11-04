using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class BaseRunner
    {

        protected static void Execute(string runner, string tool, string[] includes, string[] args)
        {
            // Execute
            try
            {
                Environment.Exit(Executor.Execute(Paths.DirName(Paths.Binary()), runner, tool, includes, args));
            }
            catch (Exception e) 
            {
                Console.Error.WriteLine("*** " + e.GetType() + ": " + e.Message);
                Environment.Exit(0xFF);
            }
        }
    }
}
