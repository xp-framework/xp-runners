using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using System.Diagnostics;

namespace Net.XpFramework.Runner
{
    class Executor
    {
        private static string PATH_SEPARATOR = new string(new char[] { Path.PathSeparator});
        private static List<string> EMPTY_LIST = new List<string>();

        /// <summary>
        /// 
        /// </summary>
        /// <param name="base_dir"></param>
        /// <param name="runner"></param>
        /// <param name="tool"></param>
        /// <param name="includes"></param>
        /// <param name="args"></param>
        public static Process Instance(string base_dir, string runner, string tool, string[] includes, string[] args)
        {
            string home = Environment.GetEnvironmentVariable("HOME");

            // Read configuration
            XpConfigSource configs = new CompositeConfigSource(
                new EnvironmentConfigSource(),
                new IniConfigSource(new Ini(Paths.Compose(".", "xp.ini"))),
                null != home ? new IniConfigSource(new Ini(Paths.Compose(home, ".xp", "xp.ini"))) : null,
                new IniConfigSource(new Ini(Paths.Compose(Environment.SpecialFolder.LocalApplicationData, "Xp", "xp.ini"))),
                new IniConfigSource(new Ini(Paths.Compose(base_dir, "xp.ini")))
            );

            IEnumerable<string> use_xp = configs.GetUse();
            string runtime = configs.GetRuntime();
            string executor = configs.GetExecutable(runtime) ?? "php";
            
            if (null == use_xp) {
                throw new EntryPointNotFoundException("Cannot determine use_xp setting from " + configs);
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
                "-C -q -dinclude_path=\".{1}{0}{1}{1}{2}\" -dmagic_quotes_gpc=0",
                String.Join(PATH_SEPARATOR, new List<string>(use_xp).ToArray()),
                PATH_SEPARATOR,
                String.Join(PATH_SEPARATOR, includes)
            );
            
            // If input or output encoding are not equal to default, also pass their
            // names inside an LC_CONSOLE environment variable. Only do this inside
            // real Windows console windows!
            //
            // See http://msdn.microsoft.com/en-us/library/system.text.encoding.headername.aspx
            // and http://msdn.microsoft.com/en-us/library/system.text.encoding.aspx
            if (null == Environment.GetEnvironmentVariable("TERM")) 
            {
                Encoding defaultEncoding = Encoding.Default;
                if (!defaultEncoding.Equals(Console.InputEncoding) || !defaultEncoding.Equals(Console.OutputEncoding)) 
                {
                    Environment.SetEnvironmentVariable("LC_CONSOLE", Console.InputEncoding.HeaderName + "," + Console.OutputEncoding.HeaderName);
                }
            }
            
            // Look for PHP configuration
            foreach (KeyValuePair<string, IEnumerable<string>> kv in configs.GetArgs(runtime))
            {
                foreach (string value in kv.Value)
                {
                    argv += " -d" + kv.Key + "=\"" + value + "\"";
                }
            }

            // Add extensions
            IEnumerable<string> extensions= configs.GetExtensions(runtime);
            if (null != extensions)
            {
                foreach (var ext in extensions)
                {
                    argv += " -dextension=" + ext;
                }
            }

            // Spawn runtime
            var proc = new Process();
            proc.StartInfo.FileName = executor;
            proc.StartInfo.Arguments = argv + " \"" + new List<string>(Paths.Locate(use_xp, "tools\\" + runner + ".php", true))[0] + "\" " + tool;
            if (args.Length > 0)
            {
                foreach (string arg in args) 
                {
                    proc.StartInfo.Arguments +=  " \"" + arg.Replace("\"", "\"\"").Replace("\\\"\"", "\\\\\\\"") + "\"";
                }
            }

            // Catch Ctrl+C (only works in "real" consoles, not in a cygwin 
            // shell, for example) and kill the spawned process, see also:
            // http://www.cygwin.com/ml/cygwin/2006-12/msg00151.html
            // http://www.mail-archive.com/cygwin@cygwin.com/msg74638.html
            Console.CancelKeyPress += delegate {
                proc.Kill();
                proc.WaitForExit();
            };
            
            proc.StartInfo.UseShellExecute = false;
            return proc;
        }

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

            var proc = Instance(base_dir, runner, tool, includes, args);
            try
            {
                proc.Start();
                proc.WaitForExit();
                return proc.ExitCode;
            }
            catch (SystemException e) 
            {
                throw new ExecutionEngineException(proc.StartInfo.FileName + ": " + e.Message, e);
            } 
            finally
            {
                proc.Close();
            }
        }
    }
}
