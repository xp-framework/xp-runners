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

        protected T AskEach<T>(Func<XpConfigSource, T> closure)
        {
            foreach (XpConfigSource source in this.sources) 
            {
                T value = closure(source);
                if (value != null) return value;
            }
            return default(T); 
        }

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
            return AskEach<IEnumerable<string>>((s) => s.GetUse());
        }

        /// <summary>
        /// Returns the runtime to be used from this config source
        /// </summary>
        public string GetRuntime()
        {
            return AskEach<string>((s) => s.GetRuntime());
        }

        /// <summary>
        /// Returns the PHP executable to be used from this config source
        /// based on the given runtime version, using the default otherwise.
        /// </summary>
        public string GetExecutable(string runtime)
        {
            return AskEach<string>((s) => s.GetExecutable(runtime));
        }

        /// <summary>
        /// Returns the PHP extensions to be loaded from this config source
        /// based on the given runtime version and the defaults.
        /// </summary>
        public IEnumerable<string> GetExtensions(string runtime)
        {
            return AskEach<IEnumerable<string>>((s) => s.GetExtensions(runtime));
        }

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// based on the given runtime version, overwriting the defaults.
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
