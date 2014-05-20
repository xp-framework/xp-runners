using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Xp : BaseRunner
    {

        static void Main(string[] args)
        {
            string[] argv;
            string[] uses = new string[] { "." };
            string tool = "";
            int shift = 0;
            var includes = new List<string>();
            includes.Add(".");
            
            if (0 == args.Length) 
            {
                tool = "xp.runtime.ShowResource";
                argv = new string[2] { "usage.txt", "255" };
            } 
            else 
            {
                for (var i = 0; i < args.Length ; i++) 
                {
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

                        case "-w": 
                        case "-d": 
                            tool = "xp.runtime.Dump"; 
                            break;

                        case "-r": 
                            tool = "xp.runtime.Reflect"; 
                            shift++; 
                            break;

                        case "-xar": 
                            tool = "xp.runtime.Xar";
                            shift++; 
                            break;

                        case "-np": 
                            uses = new string[] { };
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
                argv = new string[args.Length - shift];
                Array.Copy(args, shift, argv, 0, args.Length - shift);
            }

            // Execute
            Execute("class", tool, includes.ToArray(), argv, uses);
        }
    }
}
