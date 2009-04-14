using System;
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
        /// Returns the PHP runtime to be used from this config source
        /// </summary>
        public string GetRuntime() 
        {
            return this.ini.Get("runtime", "default");
        }

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// </summary>
        public Dictionary<string, IEnumerable<string>> GetArgs()
        {
            Dictionary<string, IEnumerable<string>> args= new Dictionary<string, IEnumerable<string>>();
            List<string> empty= new List<string>();
            foreach (string key in this.ini.Keys("runtime", empty)) {
                args[key]= this.ini.GetAll("runtime", key, empty);
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
