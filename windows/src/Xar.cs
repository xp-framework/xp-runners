using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Xar : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("class", "xp.xar.Runner", new string[] { "." }, args, new string[] { "." });
        }
    }
}
