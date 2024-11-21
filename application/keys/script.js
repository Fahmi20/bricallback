const crypto = require('crypto');
const clientID = '8kPf12Bc3HxY47RgQwZ5jT6UvRz1';
const now = new Date();
const timeStamp = now.toISOString();
const privateKey = `-----BEGIN RSA PRIVATE KEY-----
MIIEowIBAAKCAQEAhXzayvOyZ/u00vohd9jUoxtK3H86/4rmo6nF0kTO8/sT7H3c
KI6cY7nAj440LPpb5IDL+xJ5ojSBQSWAqU4RiT4xiYqpupcr5M/KznI+eI9jxkFW
n2UHx0xqvVeyQ7GY4JOkN1Vjr2xDgdCsSnbc0addYispoFuathYsoFK7uhdoii85
QS20T1YFpTdFWJtD55a4kLyCCnUxy9WjfjplgbnGS/OrhZUhVL4bYPC9+t3eXewJ
GfWNh/yG9K5wLeWTCHz2skQL5Wr8zGf+wj7BUM1EcgNQcFWJb+B4ADppxCcOXYCp
hfqvjYONIrgGgALX6abdoO1wtJVYzoqr4g2O6wIDAQABAoIBAFmQxakpTIpynAh3
Zl9osHvkQx2hjK+LvmcP8bi9DHMuA1dJt5/K2GodZ3OrAZ0wOtoeBT+oTM6mFhfl
FugChsekYE6eKHYXGo+DUNumUf5Ij7SlgH6gplB2GKSEpArBrgb5aVTrSWCZ7s26
eu/XwyA1APZuaZa8ABmu1TCZcnZQZ/1m4ZkNHFMkqCvyN/k4xDF81+meN0WzA/+x
KtRytRLG28eNWt9Qsm5oJSlUbGxh8PiAvh0SAf8myKFwoRx57QM4MTh+lF5JXVpD
5rdTOfYR/qNymZ1bLdgZbEJhxpxr84mNZDe8RHQpw8Eq0KefsEGpN1ceMUWzzSs6
GHlhi+kCgYEAxI9pjt5SgRWewMuYWmk/TzpPDY+AA69kLPiUp4HIw5FX14rtxp+Z
frFCl7oUSqvHjnSlrjHh95McnkCz3MtJd1EsoBeKkZIm94pnFAqRn/XjKXecoDgS
epcGo0s8wEbDeGGTekax2efixXdkKIALUEEbKyHB+mPji5eXXdXn3rcCgYEArdq5
YDenmwOjQft12f26kwbwyaFhYAHmGMisvHMg/JG6H4a5Iz1YwXhSjJFmFACLMwPp
OI2v7zV8WjnXvDHwkUMjt8J4hntbGZ5gOZPg77kTZpmi6IE3LjIS1GBUqZNzPyNX
FuThqQtEzvDma1ODThu58fyjGk8O5eYStA2vHW0CgYB78isQCiVgfK+kxz2FFYT0
gsJCvNBugnTa3s1uayqcF9SaeGLDsvRprYFeh9ov0+58aBXpqE7jfQK4z/gbLJ1g
/fDz6qRYcx7bTYz+WEPH6JecGG7NoU4Vu5JV+iWO4ZB1IqKKonWYAN9Awa6I02VO
8B8fraPSLpbX+XlblH0oNQKBgQChlH1h7Zf6vIDJXFqGBgmXiIXWAAUuY9VlB21z
oFTyKMahcmczV1rcRWYDe0cyI+c7vNDPXPA9FKrEeKoHISsC9zGFIls+MfvTbZzl
JomSg6KCYxxDl4SfjK5vcDB/gqlD7yaMAqGwqOaEpuSgr3eD6sUBINq+Iugnx5Nu
gKFWaQKBgBOqb2OnM/YqShlNluip3Mj+h8QNT9bPV+UgwnyJO3ExCyptlXxaU0l+
9D4IyZIUGN9fjMb6cfOo8r4q9go7Ssjobdcms9br64zBzfbQfdnzMuiF8D72xMBC
DFK0ufIF7DjE0t/rQ6c1VKL5zzIMX3Wbndl/eddxJoOUfKxf3JFJ
-----END RSA PRIVATE KEY-----`;
const stringToSign = clientID + '|' + timeStamp;
const sign = crypto.createSign('SHA256');
sign.update(stringToSign);
sign.end();
const signature = sign.sign(privateKey, 'base64');
console.log('X-CLIENT-KEY:', clientID);
console.log('X-TIMESTAMP:', timeStamp);
console.log('X-SIGNATURE:', signature);
