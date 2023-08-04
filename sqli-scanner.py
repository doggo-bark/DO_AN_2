import requests
from bs4 import BeautifulSoup
import sys
from urllib.parse import urljoin

my_session = requests.Session()
my_session.headers["User-Agent"] = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36"

"""
print(my_session.cookies.get('sessionid')) 
res=my_session.get("http://localhost/dvwa/get")
print(my_session.cookies.get('sessionid')) 
"""



"""
username="admin"
password="password"
login_url="http://localhost/DVWA/login.php"
# Get the user token from the login page
response = my_session.get(login_url)
soup = BeautifulSoup(response.text, 'html.parser')
user_token = soup.find('input', {'name': 'user_token'})['value']
  
# Set the login data
login_data = {
    'username': username,
    'password': password,
    'Login': 'Login',
    'user_token': user_token
}

# Authenticate the session by sending a POST request with the login data
response = my_session.post(login_url, data=login_data)

# Check if login was successful
if "Welcome to Damn Vulnerable Web Application!" in response.text:
    print("Login successful")
    print(f"current session:{login_data}")
else:
    print("Login failed")
"""


res=my_session.get("http://localhost/dvwa/")   # send a request to set up sessionID, a.k.a receive cookie in the response
#note that some servers may only send cookies in response to certain requests, e.g., login request

""" below listing private properties, to see public properties, check the repsonse header in developer tool
for property in my_session.cookies.__dict__:
    print(property)
print(my_session.cookies._cookies)
print(my_session.cookies._cookies_lock) """

print(my_session.cookies.get('PHPSESSID'))
print(my_session.cookies.get('security'))

# Set the security level to low
data = {'security': 'low',
        'seclev_submit': 'Submit',
        
        #check the form in source code of the security setting page
        #'user_token': ''
        }
response = my_session.post('http://localhost/DVWA/security.php', data=data)
print(my_session.cookies.get('security'))


def get_forms(url):
    soup = BeautifulSoup(my_session.get(url).content, "html.parser")
    return soup.find_all("form")
  
  
def convert_form_to_dict(form1):  # convert html to dictionary: form1 -> form2
    form2 = {}  
    action = (form1.attrs.get("action")or "").lower()
    method = (form1.attrs.get("method", "get")).lower()
    inputs = [] 
      
    for input in form1.find_all("input"):
        input_type = input.attrs.get("type", "text") # if type is not set then set "text" 
        input_name = input.attrs.get("name")
        input_value = input.attrs.get("value", "")
        inputs.append(
            {"type": input_type, "name": input_name, "value": input_value}
        ) # append dictionary to a list
          
    form2["action"] = action
    form2["method"] = method
    form2["inputs"] = inputs
    return form2
# { 'action': '/login', 
#   'method': 'post', 
#   'inputs': [ {'type': 'text', 'name': 'username', 'value': ''},
#               {'type': 'password', 'name': 'password', 'value': ''} 
#             ] 
# }
#   the value is empty since you haven't type the username and password
#   use type "password" so that they are not visible on the screen
  


def is_vulnerable(response):
    errors = {"quoted string not properly terminated",
              "unclosed quotation mark after the character string", 
              "warning: mysql",
              "you have an error in your sql syntax;"}
      
    for error in errors:
        if error in response.content.decode().lower():
            return True
    return False
  
  
def sql_injection_scan(url):
    forms = get_forms(url)
    print(f"--------------------  There are {len(forms)} forms detected on {url}  ")
      
    for form in forms:
        print(form)
        print()
        formDict = convert_form_to_dict(form)
          
        #for c in "\"'":
        for c in "'":
            payload = {}
              
            for input in formDict["inputs"]: 
                # note that there are input with type="hidden" that is not visible to user,
                # for example:
                # <input name="user_token" type="hidden" value="5c3b47e8a7923d3efad9bac879bcffb9">
                if (input["type"] != "submit") & (input["type"] != "hidden"):
                    payload[input["name"]] = input["value"] + c 
                else:
                    payload[input["name"]]=input["value"]  # other value stay the same, just change the important fields

            print(payload)       
            url = urljoin(url, formDict["action"])
              

            if formDict["method"] == "post":
                response = my_session.post(url, data=payload) 
            elif formDict["method"] == "get":
                response = my_session.get(url, params=payload) 
            
            print(response.content)
            #print(response.content.decode())
            if is_vulnerable(response):
                print("SQL Injection vulnerability detected in link:", url)
            else:
                print("No SQL Injection vulnerability detected")
                break
  
  
if __name__ == "__main__":
    target_url="http://localhost/DVWA/vulnerabilities/sqli/"
    sql_injection_scan(target_url)