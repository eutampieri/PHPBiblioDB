import json
from sys import argv
from os import rename
fileA=argv[1]
fileB=argv[2]
try:
    fileC=argv[3]
except:
    fileC=fileA
a=json.loads(open(fileA).read())
b=json.loads(open(fileB).read())
for ide,e in b.items():
    if not ide in a:
        print "Aggiunto "+e[0]
        a[ide]=e
    elif not a[ide]==e:
        print "---Record duplicati---\n"
        print "       Record A       "
        print "* "+a[ide][0]
        print "* "+a[ide][1]
        print "* "+a[ide][1]
        print "       Record B       "
        print "* "+e[0]
        print "* "+e[1]
        print "* "+e[1]
        rtk=raw_input("Record da tenere (A o B): ").lower()
        if rtk == 'b':
            a[ide]=e
            print "Salvato il record B"
        else:
            print "Mantenuto il record A"
    else:
        pass
rename(fileA,"merged-"+fileA)
rename(fileB,"merged-"+fileB)
f=open(fileC,'w')
f.write(json.dumps(a))
f.close()