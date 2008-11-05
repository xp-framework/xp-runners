using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class Doclet : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("class", "xp.doclet.Runner", new string[] { }, args);
        }
    }
}
