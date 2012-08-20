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
        /// Returns the PHP runtime to be used from this config source
        /// </summary>
        string GetRuntime();

        /// <summary>
        /// Returns whether the PHP runtime supports wmain()
        /// See http://stackoverflow.com/questions/2627891/does-process-startinfo-arguments-support-a-utf-8-string
        /// </summary>
        bool? GetWMain();

        /// <summary>
        /// Returns the PHP runtime arguments to be used from this config source
        /// </summary>
        Dictionary<string, IEnumerable<string>> GetArgs();
        
    }
}
