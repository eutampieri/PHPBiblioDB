import json
import urllib2
from os import path
import serial
class Arduino:
	arduino=serial.Serial()
	ok=False
	def __init__(self,porta,bps=9600):
		bootlog=[]
		self.arduino.port=porta
		self.arduino.baudrate=bps;
		self.arduino.open()
		self.arduino.dtr=False
		self.arduino.dtr=True
		for i in range(3):
			tmp=self.arduino.readline().strip().replace("\n",'').replace("\r",'')
			bootlog.append(tmp)
		if(bootlog[-1]=="===READY==="):
			self.ok=True
	def send(self,msg):
		for c in msg:
			self.arduino.write(c)
			pgb=self.arduino.readline().strip().replace("\n",'').replace("\r",'')
			if pgb!='1':
				raise Exception("Comando non interpretato")
		return True
	def close(self):
		self.arduino.close()
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
def regRCN(rcn):
	url="http://serverseutampieri.ddns.net/rcn.php?mode=light&register=true&ean="+str(rcn)
	try:
		r=urllib2.urlopen(url)
		if(r.read().replace('\n','')=="Aggiunto"):
			return True
		else:
			return False
	except:
		return False
def gbooks(isbn,mode):
	url="http://serverseutampieri.ddns.net/gbooks.php?mode="+mode+"&isbn="+str(isbn)
	try:
		r=urllib2.urlopen(url)
		res=r.read().replace('\n','')
		if(res!="Nessun dato"):
			return res
		else:
			return False
	except:
		return False
rj=urllib2.urlopen("http://serverseutampieri.ddns.net/rcn.json")
arr=json.loads(rj.read())
base="0"
for r,sdfg in arr.items():
	if len(r)==13 and r[0]=="2":
		if int(base)<int(r[:-1]):
			base=r[:-1]
base=int(base)+1
isbn=""
if path.isfile("libri.lbif"):
	lj=open("libri.lbif",'r')
	libri=json.loads(lj.read())
	lj.close()
else:
	libri={}
porta=raw_input("Porta seriale ETDSPL: (0 per non usarla) ")
api=raw_input("Url dell'API: ")+"/api.php"
etdspl=False;
if not porta == '0':
	etdspl=Arduino(porta)
while isbn!="stop":
	if not etdspl == False:
		etdspl.send("bg"*2)
	isbn=raw_input("ISBN: ")
	apiResp=int(urllib2.urlopen(api+"?mode=ISBNRegistered&isbn="+isbn).read())
	if apiResp>0:
		if not etdspl == False:
			etdspl.send("b")
		print "OK"
	else:
		titolo=gbooks(isbn,"titolo")
		if titolo!=False:
			if not etdspl == False:
				etdspl.send("bg"*3)
			autore=gbooks(isbn,"autore")
		else:
			if not etdspl == False:
				etdspl.send("by"*3)
			titolo=raw_input("Titolo: ")
			autore=raw_input("Autore: ")
		pos=raw_input("Posizione: ")
		libri[isbn]=[titolo,autore,pos]
		f=open("libri.lbif",'w')
		f.write(json.dumps(libri))
		f.close()
