#encoding: utf8
import json
def eanCHK(ean):
	ean=str(ean)
	count=1
	chkn=0
	for c in ean:
		if count%2==0:
			chkn=chkn+int(c)*3
		else:
			chkn=chkn+int(c)*1
		count=count+1
	if chkn%10==0:
		chkn=0
	else:
		chkn=10-chkn%10
	return ean+str(chkn)
def isEAN(ean):
	ean=str(ean)
	if ean==eanCHK(ean[:-1]):
		return True
	else:
		return False
def scheda(lista,pool):
	r=""
	for p in pool:
		try:
			r=r+p+":\tPosizione: "+lista[p][2]+", Libro: "+lista[p][0]+", "+lista[p][1]+'\n'
		except:
			r=r+p+":\tPosizione: "+lista[p][2]+", Libro di: "+lista[p][1]+'\n'
	return r
def isRCN(rcn):
	rcn=str(rcn)
	if isEAN(rcn) and rcn[0]=="2":
		return True
	else:
		return False
rcnPool=[]
o=open("libri.json",'r')
libri=json.loads(o.read())
o.close()
for i,a in libri.items():
	if isRCN(i):
		rcnPool.append(i)
rcnPool=sorted(rcnPool)
rcnApply={}
for r in rcnPool:
	rcnApply[r]=libri[r]
#print "|‾‾‾‾‾‾‾‾‾‾‾‾‾‾|\n|Riepilogo RCN:|\n|______________|"
import codecs
o=codecs.open("RCN.txt",'w','utf-8')
o.write("Riepilogo RCN:\n==============\n"+scheda(rcnApply,rcnPool))
o.close()
