# python
import base64

# https://www.pycryptodome.org/
# pip install pycryptodome
from Crypto.Cipher import AES


def decrypt(encrypted_data, session_key, iv):
    data = base64.b64decode(encrypted_data)
    _key = base64.b64decode(session_key)
    _iv = base64.b64decode(iv)

    cipher = AES.new(_key, AES.MODE_CBC, _iv)
    return cipher.decrypt(data)


if __name__  == '__main__':
    # 示例数据（加密前的数据是 "This is a secret message"）
    encrypted_data = 'Q29OV0QQS8eL7mLsl4cCcdkS6kXhA5DPnbZcMgO5vhs='
    session_key = 'fYpxIqE8eIBKURTpr2GfZXK4pqTiQ+U9+9cM2GRF6gk='
    iv = 'xeybFIS3EtD1zmgcBfVRZw=='
    print(decrypt(encrypted_data, session_key, iv))
