using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Unittest : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("cli", "xp.unittest.Runner", new string[] { "." }, args);
        }
    }
}
