using System;
using System.Management;
using System.Diagnostics;

namespace Net.XpFramework.Runner
{
    class Monitor
    {

        static void Main(string[] args)
        {
            string wql = String.Format("TargetInstance isa 'Win32_Process' and TargetInstance.ProcessId = {0}", args[0]);
            int child = Convert.ToInt32(args[1]);
           
            using (var watcher = new ManagementEventWatcher()) 
            {
                watcher.Query= new WqlEventQuery("__InstanceDeletionEvent", new TimeSpan(0, 0, 0, 1), wql);
                watcher.WaitForNextEvent();
                
                // Parent process was killed, kill child if necessary
                try 
                {
                    Process.GetProcessById(child).Kill();
                }
                catch (ArgumentException)
                {
                    // Ignore, child terminated between event triggering and our lookup
                }
                
                watcher.Stop();
            }
        }
    }
}
