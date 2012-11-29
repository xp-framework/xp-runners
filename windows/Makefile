ifeq ($(OS),Windows_NT)
	BASE=$(shell cygpath "$(WINDIR)")
	CSC?=$(BASE)/Microsoft.NET/Framework/v3.5/csc
else
	CSC?=csc
endif
CLASSES=src\\AssemblyInfo.cs src\\Executor.cs src\\Paths.cs src\\BaseRunner.cs src\\XpConfigSource.cs src\\EnvironmentConfigSource.cs src\\IniConfigSource.cs src\\CompositeConfigSource.cs src\\Ini.cs
CSC_OPT=/nologo /target:exe
all: xp.exe xpcli.exe xpws.exe xar.exe unittest.exe cgen.exe doclet.exe xcc.exe xpi.exe

xp.exe: src\\Xp.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Xp.cs $(CLASSES)

xpcli.exe: src\\XpCli.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\XpCli.cs $(CLASSES)

xpws.exe: src\\XpWs.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\XpWs.cs $(CLASSES)

xar.exe: src\\Xar.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Xar.cs $(CLASSES)

unittest.exe: src\\Unittest.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Unittest.cs $(CLASSES)

cgen.exe: src\\Cgen.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Cgen.cs $(CLASSES)

doclet.exe: src\\Doclet.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Doclet.cs $(CLASSES)

xcc.exe: src\\Xcc.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Xcc.cs $(CLASSES)

xpi.exe: src\\Xpi.cs $(CLASSES)
	$(CSC) $(CSC_OPT) /out:$@ src\\Xpi.cs $(CLASSES)

clean:
	-rm xp.exe xpcli.exe xpws.exe xar.exe unittest.exe cgen.exe doclet.exe xcc.exe xpi.exe

