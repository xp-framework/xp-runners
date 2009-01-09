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
        /// Returns the PHP runtime to be used from this config source
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
