using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Xcc : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("cli", "xp.compiler.Runner", new string[] { "." }, args);
        }
    }
}
