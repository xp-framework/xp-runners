using System;
using System.Collections.Generic;
using System.Linq;
using System.IO;

namespace Net.XpFramework.Runner
{
    class Paths
    {
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
            foreach (string path in bases)
            {

                string qualified = path + Path.DirectorySeparatorChar + file;
                if (File.Exists(qualified))
                {
                    yield return qualified;
                }
            }
            if (expect)
            {
                throw new FileNotFoundException("Cannot find " + file + " in [" + String.Join(", ", bases.ToArray()) + "]");
            }
        }

        /// <summary>
        /// Translate a list of paths
        /// </summary>
        /// <param name="root"></param>
        /// <param name="paths"></param>
        /// <returns></returns>
        public static IEnumerable<string> Translate(string root, string[] paths)
        {
            var HOME = Environment.GetEnvironmentVariable("HOME") ?? Environment.GetFolderPath(Environment.SpecialFolder.Personal);

            foreach (string path in paths)
            {
                if (path.StartsWith("~"))
                {
                    // Path in home directory
                    yield return HOME + path.Substring(1);
                } 
                else if (path.Substring(1).StartsWith(":\\") || path.StartsWith("\\\\")) 
                {
                    // Fully qualified path
                    yield return path;
                }
                else
                {
                    // Relative path, prepend root
                    yield return root + Path.DirectorySeparatorChar + path;
                }
            }
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
