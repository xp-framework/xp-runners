using System;
using System.Collections.Generic;

namespace Net.XpFramework.Runner
{
    class XpCli : BaseRunner
    {

        static void Main(string[] args)
        {
            Execute("class", "xp.command.Runner", new string[] { "." }, args, new string[] { "." });
        }
    }
}
