using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using System.Diagnostics;
using System.Runtime.InteropServices;


namespace Net.XpFramework.Runner
{
    delegate string ArgProcessor(string arg);
    
    class Executor
    {
        private static string PATH_SEPARATOR = new string(new char[] { Path.PathSeparator});
        private static List<string> EMPTY_LIST = new List<string>();

        [DllImport("kernel32.dll", CharSet = CharSet.Auto, SetLastError = true)] 
        internal static extern IntPtr GetStdHandle(int nStdHandle); 

        [DllImport("kernel32.dll", SetLastError = true)]
        internal static extern bool GetConsoleMode(IntPtr hConsoleHandle, out int mode);
        
        internal enum StandardHandles 
        {
            IN  = -10,
            OUT = -11,
            ERR = -12
        }
        
        public static bool IsRedirect(StandardHandles id)
        {
            int mode;
            
            if (!GetConsoleMode(GetStdHandle((int)id), out mode))
            {
                throw new IOException("GetConsoleMode(" + id + ") failed");
            }

            return 0 == mode;
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
            string executor = configs.GetRuntime() ?? "php";
            bool wmain = configs.GetWMain() ?? false;
            
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
            
            // Pass input,output,default encodings and whether stdin,out and err are redirects
            // or not via LC_CONSOLE. Only do this inside real Windows console windows, for
            // Cygwin, this works quite differently!
            //
            // See http://msdn.microsoft.com/en-us/library/system.text.encoding.headername.aspx
            // and http://msdn.microsoft.com/en-us/library/system.text.encoding.aspx
            if (null == Environment.GetEnvironmentVariable("TERM")) 
            {
                Encoding defaultEncoding = Encoding.Default;
                Environment.SetEnvironmentVariable("LC_CONSOLE", String.Format(
                    "{0} {1} {2} {3}{4}{5}",
                    Console.InputEncoding.HeaderName,
                    Console.OutputEncoding.HeaderName,
                    wmain ? "utf-16" : "utf-8",
                    IsRedirect(StandardHandles.IN) ? 1 : 0,
                    IsRedirect(StandardHandles.OUT) ? 1 : 0,
                    IsRedirect(StandardHandles.ERR) ? 1 : 0
                ));
            }
            else
            {
                string lang = Environment.GetEnvironmentVariable("LANG");
                Environment.SetEnvironmentVariable("LC_CONSOLE", String.Format(
                    "{0} {0} {1} 000",
                    null == lang ? Encoding.Default.HeaderName : lang.Split('.')[1],
                    wmain ? "utf-16" : "utf-8"
                ));
            }
            
            // Look for PHP configuration
            foreach (KeyValuePair<string, IEnumerable<string>> kv in configs.GetArgs())
            {
                foreach (string value in kv.Value)
                {
                    argv += " -d" + kv.Key + "=\"" + value + "\"";
                }
            }

            // Spawn runtime
            var proc = new Process();
            proc.StartInfo.FileName = executor;
            proc.StartInfo.Arguments = argv + " \"" + new List<string>(Paths.Locate(use_xp, "tools\\" + runner + ".php", true))[0] + "\" " + tool;
            if (args.Length > 0)
            {
                ArgProcessor process;

                // Workaround problem that php itself doesn't have a wmain() method, so the OS
                // converts the strings passed as argument to OS default encoding (CP1252, on
                // a German Windows installation). Works fine until someone enters Japanese 
                // characters). Convert to single byte character set before passing. 
                //
                // This workaround can be disabled by setting runtime.wmain to true once PHP
                // declares that. See http://msdn.microsoft.com/en-us/library/88w63h9k.aspx
                if (wmain) 
                {
                    process = delegate(string arg) 
                    { 
                        return arg; 
                    };
                }
                else
                {
                    process = delegate(string arg) 
                    { 
                        var mb = Encoding.UTF8;
                        var sb = Encoding.GetEncoding(1252); 
                    
                        return sb.GetString(mb.GetBytes(arg)); 
                    };
                }
                foreach (string arg in args) 
                {
                    proc.StartInfo.Arguments +=  " \"" + process(arg.Replace("\"", "\"\"\"")) + "\"";
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
            try
            {
                proc.Start();
                proc.WaitForExit();
                return proc.ExitCode;
            }
            catch (SystemException e) 
            {
                throw new ExecutionEngineException(executor + ": " + e.Message, e);
            } 
            finally
            {
                proc.Close();
            }
        }
    }
}
