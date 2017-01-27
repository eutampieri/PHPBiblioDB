# the block size for the cipher object; must be 16, 24, or 32 for AES
BLOCK_SIZE = 32
 
# the character used for padding--with a block cipher such as AES, the value
# you encrypt must be a multiple of BLOCK_SIZE in length. This character is
# used to ensure that your value is always a multiple of BLOCK_SIZE
PADDING = '{'
 
# one-liner to sufficiently pad the text to be encrypted
pad = lambda s: s + (BLOCK_SIZE - len(s) % BLOCK_SIZE) * PADDING
 
# one-liners to encrypt/encode and decrypt/decode a string
# encrypt with AES, encode with base64
EncodeAES = lambda c, s: base64.b64encode(c.encrypt(pad(s)))
DecodeAES = lambda c, e: c.decrypt(base64.b64decode(e)).rstrip(PADDING)

try:
	secret=os.popen("./jsonvalidator.out").read()
except:
	os.system("./keygen.sh")
	secret=os.popen("./jsonvalidator.out").read()
# create a cipher object using the random secret
cipher = AES.new(secret)
 
def encode(string):
	encoded = EncodeAES(cipher, string)
	return encoded 
# decode the encoded string
def decode(encoded):
	decoded = DecodeAES(cipher, encoded)
	return decoded 
