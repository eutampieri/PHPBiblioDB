from json import loads
a=0
posizione=[]
libri=loads(open(raw_input("Nome del file: "),"r").read())
for i,l in libri.items():
	a=a+1
	if not l[2]in posizione:
		posizione.append(l[2])
	print l[0]+"\t"+l[1]
pos=""
for p in posizione:
	pos =pos+p+", "
print str(a)+" libri in "+str(len(posizione))+" posizioni ("+pos[:-2]+")"
