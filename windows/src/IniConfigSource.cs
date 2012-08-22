﻿using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Net.XpFramework.Runner
{
    class IniConfigSource : XpConfigSource 
    {
        private Ini ini;
    
        /// <summary>
        /// Constructor
        /// </summary>
        /// <param name="ini"></param>
        public IniConfigSource(Ini ini) 
        {
            this.ini = ini;
        }
        
        /// <summary>
        /// Returns the use_xp setting derived from this config source
        /// </summary>
        public IEnumerable<string> GetUse() 
        {
            string value = this.ini.Get("default", "use");
            return null == value ? null : Paths.Translate(
                Paths.DirName(this.ini.FileName),
                value.Split(new char[] { Path.PathSeparator })
            );
        }

        /// <summary>
        /// Returns the runtime to be used from this config source
        /// </summary>
        public string GetRuntime()
        {
            return this.ini.Get("default", "rt");
        }

        /// <summary>
        /// Returns the PHP executable to be used from this config source
        /// based on the given runtime version.
        /// </summary>
        public string GetExecutable(string runtime) 
        {
            return this.ini.Get("runtime@" + runtime, "default") ?? this.ini.Get("runtime", "default");
        }

        /// <summary>
        /// Returns all keys in a given section as key/value pair
        /// </summary>
        protected IEnumerable<KeyValuePair<string, IEnumerable<string>>> ArgsInSection(string section)
        {
            List<string> empty= new List<string>();
            foreach (string key in this.ini.Keys(section, empty))
            {
                if (!"default".Equals(key))
                {
                    yield return new KeyValuePair<string, IEnumerable<string>>(key, this.ini.GetAll(section, key, empty));
                }
            }
        }

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// based on the given runtime version.
        /// </summary>
        public Dictionary<string, IEnumerable<string>> GetArgs(string runtime)
        {
            Dictionary<string, IEnumerable<string>> args= new Dictionary<string, IEnumerable<string>>();
            foreach (var pair in ArgsInSection("runtime"))
            {
                args[pair.Key]= pair.Value;
            }
            foreach (var pair in ArgsInSection("runtime@" + runtime))
            {
                args[pair.Key]= pair.Value;
            }
            return args;
        }
        
        /// <summary>
        /// Returns a string representation of this config source
        /// </summary>
        public override string ToString() 
        {
            return new StringBuilder(this.GetType().FullName)
                .Append("<")
                .Append(this.ini.FileName)
                .Append(">")
                .ToString()
            ;
        }
    }
}
