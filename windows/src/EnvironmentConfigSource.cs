﻿using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Net.XpFramework.Runner
{
    class EnvironmentConfigSource : XpConfigSource 
    {
        /// <summary>
        /// Returns whether this config source is valid
        /// </summary>
        public bool Valid() {
            return true;
        }

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
        /// based on the given runtime version, using the default otherwise.
        /// </summary>
        public string GetExecutable(string runtime) 
        {
            return null;
        }

        /// <summary>
        /// Returns the paths to load XP modules from based on the given
        /// runtime version and the defaults.
        /// </summary>
        public IEnumerable<string> GetModules(string runtime)
        {
            return null;
        }

        /// <summary>
        /// Returns the PHP extensions to be loaded from this config source
        /// based on the given runtime version and the defaults.
        /// </summary>
        public IEnumerable<string> GetExtensions(string runtime)
        {
            return null;
        }

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// based on the given runtime version, overwriting the defaults.
        /// </summary>
        public Dictionary<string, IEnumerable<string>> GetArgs(string runtime)
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
