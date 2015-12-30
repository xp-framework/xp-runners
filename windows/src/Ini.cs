using System;
using System.Collections.Generic;
using System.Text;
using System.IO;
using System.Linq;

namespace Net.XpFramework.Runner
{
    class Ini
    {
        private static int KEY = 0;
        private static int VALUE = 1;
        private static char[] KVAL_SEPARATOR = new char[] { '=' };
        private static char[] PATH_SEPARATOR = new char[] { Path.PathSeparator };
        private string file;
        private Dictionary<string, Dictionary<string, List<string>>> sections;
        private bool parsed;

        public string FileName 
        {
            get { return this.file; }
            set { 
                this.file = value; 
                this.sections = new Dictionary<string, Dictionary<string, List<string>>>();
                this.parsed = false;
            }
        }

        public Ini(string file) 
        {
            this.FileName = file;
        }

        public bool Exists()
        {
            return File.Exists(this.FileName);
        }

        public void Parse(bool reset)
        {
            lock(this) 
            {
                if (this.parsed && !reset) return;    // Short-circuit this
                if (!this.Exists()) return;

                string section = "default";
                this.sections[section] = new Dictionary<string, List<string>>();
                foreach (string line in File.ReadAllLines(this.FileName))
                {
                    if (line == "" || line.StartsWith(";")) 
                    {
                        continue;
                    } 
                    else if (line.StartsWith("[")) 
                    {
                        section = line.Substring(1, line.Length - 1 - 1);    
                        this.sections[section] = new Dictionary<string, List<string>>();
                        continue;
                    } else {
                        string[] parsed = line.Split(KVAL_SEPARATOR, 2);
                        if (!this.sections[section].ContainsKey(parsed[KEY])) 
                        {
                            this.sections[section][parsed[KEY]] = new List<string>();
                        }
                        if (!String.IsNullOrEmpty(parsed[VALUE]))
                        {
                            this.sections[section][parsed[KEY]].Add(parsed[VALUE]);
                        }
                    }
                }
                this.parsed = true;
            }
        }
 
        public string Get(string section, string key, string defaultValue) {
            this.Parse(false);
            if (!this.sections.ContainsKey(section)) return defaultValue;
            if (!this.sections[section].ContainsKey(key)) return defaultValue;
            return this.sections[section][key].FirstOrDefault();
        }   

        public string Get(string section, string key) {
            return this.Get(section, key, null);
        }

        public IEnumerable<string> GetAll(string section, string key, IEnumerable<string> defaultValue) {
            this.Parse(false);
            if (!this.sections.ContainsKey(section)) return defaultValue;
            if (!this.sections[section].ContainsKey(key)) return defaultValue;
            return this.sections[section][key];
        }   

        public IEnumerable<string> GetAll(string section, string key) {
            return this.GetAll(section, key, null);
        }

        public IEnumerable<string> Keys(string section, IEnumerable<string> defaultValue) {
            this.Parse(false);
            if (!this.sections.ContainsKey(section)) return defaultValue;
            return this.sections[section].Keys;
        }

        public IEnumerable<string> Keys(string section) {
            return this.Keys(section, null);
        }
    }
}
