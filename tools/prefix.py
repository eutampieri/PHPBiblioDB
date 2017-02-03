import json
from sys import argv
from os import rename
fileA=argv[1]
a=json.loads(open(fileA).read())
prefix=raw_input("Prefisso: ")
for ide,e in a.items():
    if e[2].find(prefix)==-1:
        a[ide][2]=prefix+a[ide][2]
rename(fileA,"old-"+fileA)
f=open(fileA,'w')
f.write(json.dumps(a))
f.close()