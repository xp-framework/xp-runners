CSC?=csc
CLASSES=src\\Executor.cs src\\Paths.cs src\\BaseRunner.cs

all: xp.exe xpcli.exe xar.exe unittest.exe cgen.exe doclet.exe

xp.exe: src\\Xp.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ src\\Xp.cs $(CLASSES)

xpcli.exe: src\\XpCli.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ src\\XpCli.cs $(CLASSES)

xar.exe: src\\Xar.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ src\\Xar.cs $(CLASSES)

unittest.exe: src\\Unittest.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ src\\Unittest.cs $(CLASSES)

cgen.exe: src\\Cgen.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ src\\Cgen.cs $(CLASSES)

doclet.exe: src\\Doclet.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ src\\Doclet.cs $(CLASSES)

clean:
	-rm xp.exe xpcli.exe xar.exe unittest.exe cgen.exe doclet.exe

