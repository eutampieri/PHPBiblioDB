import json
mf=open(raw_input("Database di origine: "),'r')
df=open("bibliodb.json",'r')
libri=json.loads(mf.read())
bibliodb=json.loads(df.read())
df.close()
mf.close()
prefisso=raw_input("Prefisso posizione: ")
for i,l in libri.items():
	bibliodb[1][i]=prefisso+l[2].upper()
	bibliodb[2][l[0].lower()]=i
	bibliodb[3][i]=l[0].lower()
	bibliodb[4][i]=l[1].lower()
	bibliodb[6][i]="Biblioteca"
df=open("bibliodb.json",'w')
df.write(json.dumps(bibliodb))
df.close()