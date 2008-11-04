using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Xp : BaseRunner
    {

        static void Main(string[] args)
        {
            var tool = "";
            var runner = "class";
            var shift = 0;
            var includes = new List<string>();

            for (var i = 0; i < args.Length ; i++) {
                switch (args[i])
                {
                    case "-v": 
                        tool = "xp.runtime.Version"; 
                        shift++;  
                        break;

                    case "-e": 
                        tool = "xp.runtime.Evaluate"; 
                        shift++; 
                        break;

                    case "-xar": 
                        runner = "xar"; 
                        shift++; 
                        break;

                    case "-cp":
                        includes.Add(args[++i]);
                        shift += 2;
                        break;

                    default:
                        if (args[i].StartsWith("-"))
                        {
                            Console.Error.WriteLine("*** Invalid argument {0}", args[i]);
                            Environment.Exit(0xFF);
                        } 
                        else 
                        {
                            i = args.Length;
                        }
                        break;
                }
            }

            // Shift
            var argv = new string[args.Length - shift];
            Array.Copy(args, shift, argv, 0, args.Length - shift);

            // Execute
            Execute(runner, tool, includes.ToArray(), argv);
        }
    }
}
