using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Net.XpFramework.Runner
{
    class EnvironmentConfigSource : XpConfigSource 
    {
        /// <summary>
        /// Returns the use_xp setting derived from this config source
        /// </summary>
        public IEnumerable<string> GetUse() 
        {
            string env = Environment.GetEnvironmentVariable("USE_XP");
            return env == null ? null : Paths.Translate(
                Environment.CurrentDirectory, 
                env.Split(new char[] { Path.PathSeparator })
            );
        }
        
        /// <summary>
        /// Returns the runtime to be used from this config source
        /// </summary>
        public string GetRuntime() 
        {
            return Environment.GetEnvironmentVariable("XP_RT");
        }

        /// <summary>
        /// Returns the PHP executable to be used from this config source
        /// </summary>
        public string GetExecutable() 
        {
            return null;
        }

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// </summary>
        public Dictionary<string, IEnumerable<string>> GetArgs()
        {
            return null;
        }

        /// <summary>
        /// Returns a string representation of this config source
        /// </summary>
        public override string ToString() 
        {
            return this.GetType().FullName;
        }
    }
}
