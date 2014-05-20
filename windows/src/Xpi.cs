using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Xpi : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("class", "xp.install.Runner", new string[] { "." }, args, new string[] { "." });
        }
    }
}
