using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Net.XpFramework.Runner
{
    class CompositeConfigSource : XpConfigSource 
    {
        private List<XpConfigSource> sources;
        
        public CompositeConfigSource(params XpConfigSource[] sources) 
        {
            this.sources = new List<XpConfigSource>(sources);
            this.sources.RemoveAll(delegate(XpConfigSource o) { return o == null; });
        }
    
        /// <summary>
        /// Returns the use_xp setting derived from this config source
        /// </summary>
        public IEnumerable<string> GetUse() 
        {
            foreach (XpConfigSource source in this.sources) 
            {
                IEnumerable<string> use= source.GetUse();
                if (use != null) return use;
            }
            return null;
        }

        /// <summary>
        /// Returns the runtime to be used from this config source
        /// </summary>
        public string GetRuntime()
        {
            foreach (XpConfigSource source in this.sources) 
            {
                string runtime = source.GetRuntime();
                if (runtime != null) return runtime;
            }
            return null;
        }

        /// <summary>
        /// Returns the PHP executable to be used from this config source
        /// based on the given runtime version.
        /// </summary>
        public string GetExecutable(string runtime)
        {
            foreach (XpConfigSource source in this.sources) 
            {
                string executable = source.GetExecutable(runtime);
                if (executable != null) return executable;
            }
            return null;
        }

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// based on the given runtime version.
        /// </summary>
        public Dictionary<string, IEnumerable<string>> GetArgs(string runtime)
        {
            Dictionary<string, IEnumerable<string>> merged= new Dictionary<string, IEnumerable<string>>();
            foreach (XpConfigSource source in this.sources) 
            {
                Dictionary<string, IEnumerable<string>> args = source.GetArgs(runtime);
                if (args == null) continue;

                foreach (KeyValuePair<string, IEnumerable<string>> kv in args) 
                {
                    if (!merged.ContainsKey(kv.Key)) 
                    {
                        merged[kv.Key] = kv.Value;
                    }
                }
            }
            return merged;
        }
        
        /// <summary>
        /// Returns a string representation of this config source
        /// </summary>
        public override string ToString() 
        {
            var buffer = new StringBuilder(this.GetType().FullName);
            buffer.Append("@{\n");
            foreach (XpConfigSource source in this.sources) 
            {
                buffer.Append("  " + source + "\n");
            }
            return buffer.Append("}").ToString(); 
        }
    }
}
