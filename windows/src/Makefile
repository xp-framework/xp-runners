CSC?=csc
CLASSES=Executor.cs Paths.cs BaseRunner.cs

all: xp.exe xpcli.exe xar.exe unittest.exe cgen.exe doclet.exe

xp.exe: Xp.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ Xp.cs $(CLASSES)

xpcli.exe: XpCli.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ XpCli.cs $(CLASSES)

xar.exe: Xar.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ Xar.cs $(CLASSES)

unittest.exe: Unittest.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ Unittest.cs $(CLASSES)

cgen.exe: Cgen.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ Cgen.cs $(CLASSES)

doclet.exe: Doclet.cs $(CLASSES)
	$(CSC) /nologo /target:exe /out:$@ Doclet.cs $(CLASSES)

clean:
	-rm xp.exe
