using System;
using System.Collections.Generic;
using System.Text;
using System.IO;

namespace Net.XpFramework.Runner
{
    interface XpConfigSource 
    {

        /// <summary>
        /// Returns the use_xp setting derived from this config source
        /// </summary>
        IEnumerable<string> GetUse();

        /// <summary>
        /// Returns the runtime to be used from this config source
        /// </summary>
        string GetRuntime();

        /// <summary>
        /// Returns the PHP executable to be used from this config source
        /// based on the given runtime version, using the default otherwise.
        /// </summary>
        string GetExecutable(string runtime);

        /// <summary>
        /// Returns the PHP extensions to be loaded from this config source
        /// based on the given runtime version and the defaults.
        /// </summary>
        IEnumerable<string> GetExtensions(string runtime);

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// based on the given runtime version, overwriting the defaults.
        /// </summary>
        Dictionary<string, IEnumerable<string>> GetArgs(string runtime);
    }
}
