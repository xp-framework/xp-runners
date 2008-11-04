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
            var env = System.Environment.GetEnvironmentVariable("USE_XP");
            IEnumerable<string> use_xp = null;
            if (null == env)
            {
                if (!File.Exists(base_dir + "xp.ini"))
                {
                    throw new FileNotFoundException("Cannot find xp.ini in " + base_dir);
                }

                foreach (var line in File.ReadAllLines(base_dir + "xp.ini"))
                {
                    var parsed = line.Split(KVAL_SEPARATOR, 2);
                    if (parsed[KEY] == "use")
                    {
                        use_xp = Paths.Translate(base_dir, parsed[VALUE].Split(PATH_SEPARATOR));
                    }
                }
            }
            else
            {
                use_xp = Paths.Translate(System.Environment.CurrentDirectory, env.Split(PATH_SEPARATOR));
            }
            
            // Search for tool
            var executor = "php";
            var argv = String.Format(
                "-dinclude_path=\".;{0}\" -duser_dir=\"{1}\" -dmagic_quotes_gpc=0",
                String.Join(new string(PATH_SEPARATOR), includes),
                String.Join(new string(PATH_SEPARATOR), use_xp.ToArray())
            );
            foreach (var ini in Paths.Locate(use_xp, "php.ini", false))
            {
                foreach (var line in File.ReadAllLines(ini))
                {
                    var parsed = line.Split(KVAL_SEPARATOR, 2);
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
