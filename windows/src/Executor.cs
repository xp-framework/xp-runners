using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.IO;

namespace Net.XpFramework.Runner
{
    class Executor
    {
        private static int KEY = 0;
        private static int VALUE = 1;
        private static char[] PATH_SEPARATOR = new char[] { Path.PathSeparator };
        private static char[] KVAL_SEPARATOR = new char[] { '=' };

        /// <summary>
        /// 
        /// </summary>
        /// <param name="base_dir"></param>
        /// <param name="runner"></param>
        /// <param name="tool"></param>
        /// <param name="includes"></param>
        /// <param name="args"></param>
        public static int Execute(string base_dir, string runner, string tool, string[] includes, string[] args)
        {
            // Determine USE_XP path from either environment option or from xp.ini
            string env = Environment.GetEnvironmentVariable("USE_XP");
            string executor = Environment.GetEnvironmentVariable("XP_RT") ?? "php";
            IEnumerable<string> use_xp = null;
            if (null == env)
            {
                if (!File.Exists(base_dir + "xp.ini"))
                {
                    throw new FileNotFoundException("Cannot find xp.ini in " + base_dir);
                }

                string section = "default";
                foreach (string line in File.ReadAllLines(base_dir + "xp.ini"))
                {
                    if (line == "" || line.StartsWith(";")) continue;

                    string[] parsed = line.Split(KVAL_SEPARATOR, 2);
                    if (parsed[KEY].StartsWith("[")) {
                        section = parsed[KEY].Substring(1, parsed[KEY].Length - 1 - 1);
                        continue;
                    }

                    switch (section)
                    {
                        case "default":
                            if ("use" == parsed[KEY])
                            {
                                use_xp = Paths.Translate(base_dir, parsed[VALUE].Split(PATH_SEPARATOR));
                                break;
                            }
                            goto default;

                        case "runtime":
                            executor = parsed[VALUE];
                            break;

                        default:
                            throw new FormatException("Unknown key '" + parsed[KEY] + "' in " + section + " section");
                    }                }
            }
            else
            {
                use_xp = Paths.Translate(System.Environment.CurrentDirectory, env.Split(PATH_SEPARATOR));
            }
            
            // Pass "USE_XP" and includes inside include_path separated by two path 
            // separators. Prepend "." for the oddity that if the first element does
            // not exist, PHP scraps all the others(!)
            //
            // E.g.: -dinclude_path=".;xp\5.7.0;..\dialog;;..\impl.xar;..\log.xar"
            //                       ^ ^^^^^^^^^^^^^^^^^^  ^^^^^^^^^^^^^^^^^^^^^^
            //                       | |                   include_path
            //                       | USE_XP
            //                       Dot
            string argv = String.Format(
                "-dinclude_path=\".{1}{0}{1}{1}{2}\" -dmagic_quotes_gpc=0",
                String.Join(new string(PATH_SEPARATOR), use_xp.ToArray()),
                new string(PATH_SEPARATOR),
                String.Join(new string(PATH_SEPARATOR), includes)
            );
            foreach (string ini in Paths.Locate(use_xp, "php.ini", false))
            {
                foreach (string line in File.ReadAllLines(ini))
                {
                    string[] parsed = line.Split(KVAL_SEPARATOR, 2);
                    if (parsed[KEY] == "executor")
                    {
                        executor = parsed[VALUE];
                    }
                    else
                    {
                        argv += " -d" + parsed[KEY] + "=\"" + parsed[VALUE] + "\"";
                    }
                }
            }

            // Spawn runtime
            var proc = new System.Diagnostics.Process();
            proc.StartInfo.FileName = executor;
            proc.StartInfo.Arguments = argv + " \"" + Paths.Locate(use_xp, "tools\\" + runner + ".php", true).First() + "\" " + tool;
            if (args.Length > 0)
            {
                proc.StartInfo.Arguments +=  " \"" + String.Join("\" \"", args) + "\"";
            }
            
            proc.StartInfo.UseShellExecute = false;
            try
            {
                proc.Start();
                proc.WaitForExit();
                return proc.ExitCode;
            }
            finally
            {
                proc.Close();
            }
        }
    }
}
