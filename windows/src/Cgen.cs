using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Cgen : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("class", "xp.codegen.Runner", new string[] { "." }, args, new string[] { "." });
        }
    }
}
