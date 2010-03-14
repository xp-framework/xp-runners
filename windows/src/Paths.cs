using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using WshShell = IWshRuntimeLibrary.WshShell;
using IWshShortcut = IWshRuntimeLibrary.IWshShortcut;

namespace Net.XpFramework.Runner
{
    class Paths
    {
        private static WshShell shell = null;

        /// <summary>
        /// Returns the directory name of a given file name
        /// </summary>
        /// <param name="filename"></param>
        /// <returns></returns>
        public static string DirName(string filename)
        {
            return filename.Substring(0, filename.LastIndexOf(Path.DirectorySeparatorChar) + 1);
        }

        /// <summary>
        /// Locate a given file inside multiple base paths. Similar to what is done when
        /// looking up program names in $ENV{PATH}.
        /// </summary>
        /// <param name="bases"></param>
        /// <param name="file"></param>
        /// <param name="expect">Whether we expect a non-empty list</param>
        /// <returns></returns>
        public static IEnumerable<string> Locate(IEnumerable<string> bases, string file, bool expect)
        {
            bool found = false;
            foreach (string path in bases)
            {
                string qualified = path.TrimEnd(Path.DirectorySeparatorChar) + Path.DirectorySeparatorChar + file;
                
                if (File.Exists(qualified))
                {
                    found = true;
                    yield return qualified;
                }

            }
            if (expect && !found)
            {
                throw new FileNotFoundException("Cannot find " + file + " in [" + String.Join(", ", new List<string>(bases).ToArray()) + "]");
            }
        }
        
        /// <summary>
        /// Resolve a path. If the path is actually a shell link (.lnk file), this link's target path
        /// is used.
        /// </summary>
        /// <param name="path"></param>
        /// <returns></returns>
        public static string Resolve(string path)
        {
            if (!File.Exists(path)) 
            {
                string link = path.TrimEnd(Path.DirectorySeparatorChar) + ".lnk";
                if (File.Exists(link)) 
                {
                    shell = shell ?? new WshShell();   // Lazy initialization
                    return (shell.CreateShortcut(link) as IWshShortcut).TargetPath;
                }
            }
            return path;
        }
        
        /// <summary>
        /// Translate a list of paths
        /// </summary>
        /// <param name="root"></param>
        /// <param name="paths"></param>
        /// <returns></returns>
        public static IEnumerable<string> Translate(string root, string[] paths)
        {
            string HOME = Environment.GetEnvironmentVariable("HOME") ?? Environment.GetFolderPath(Environment.SpecialFolder.Personal);

            foreach (string path in paths)
            {
                // Normalize path
                string normalized = path.Replace('/', Path.DirectorySeparatorChar);

                if (normalized.StartsWith("~"))
                {
                    // Path in home directory
                    yield return Resolve(Compose(HOME, normalized.Substring(1)));
                } 
                else if (normalized.Substring(1).StartsWith(":\\") || normalized.StartsWith("\\\\")) 
                {
                    // Fully qualified path
                    yield return Resolve(normalized);
                }
                else
                {
                    // Relative path, prepend root
                    yield return Resolve(Compose(root, normalized));
                }
            }
        }
        
        /// <summary>
        /// Composes a path name of two or more components - varargs
        /// </summary>
        /// <param name="components"></param>
        /// <returns></returns>
        public static string Compose(params string[] components) 
        {
            var s = new StringBuilder();
            foreach (string component in components) {
                s.Append(component.TrimEnd(new char[] { Path.DirectorySeparatorChar })).Append(Path.DirectorySeparatorChar);
            }
            s.Length--;           // Remove last directory separator
            return s.ToString();
        }
        
        /// <summary>
        /// Composes a path name of a special folder and a string component
        /// </summary>
        /// <param name="kind"></param>
        /// <param name="file"></param>
        /// <returns></returns>
        public static string Compose(Environment.SpecialFolder special, string component) 
        {
            return Compose(Environment.GetFolderPath(special), component);
        }

        /// <summary>
        /// Return binary file of currently executing process
        /// </summary>
        /// <returns></returns>
        public static string Binary()
        {
            // Codebase is a URI. file:///F:/cygwin/home/Timm Friebe/bin/xp.exe
            var uri = new Uri(System.Reflection.Assembly.GetExecutingAssembly().CodeBase);
            if (uri.IsFile)
            {
                return Uri.UnescapeDataString(uri.AbsolutePath.Replace('/', Path.DirectorySeparatorChar));
            }
            else
            {
                throw new IOException("Don't know how to handle " + uri.AbsoluteUri);
            }
        }
    }
}
