#include <WiFi.h>
#include <HTTPClient.h>
 
const char* ssid = "your_WiFi_SSID";
const char* password =  "your_WiFi_Password";
const char* url =  "http://peacock.work/rss.php";
String payload_pattern = "{\"username\":\"someusername\",\"password\":\"somepassword\",\"title\":\"ทดสอบภาษาไทย\",\"link\":\"www.test.com\",\"description\":\"กุ้งกั้ง\",\"pubdate\":\"2020-01-01\"}";


void setup() {
 
  Serial.begin(115200);
  delay(4000);   //Delay needed before calling the WiFi.begin
 
  WiFi.begin(ssid, password); 
 
  while (WiFi.status() != WL_CONNECTED) { //Check for the connection
    delay(1000);
    Serial.println("Connecting to WiFi..");
  }
 
  Serial.println("Connected to the WiFi network");
   
  //float temperature = 23.456;
  //float humidity = 80.21;
  String payload = payload_pattern;
  //payload.replace("$temperature$",String(temperature));
  //payload.replace("$humidity$",String(humidity));
  //payload.replace("$counter$",String(counter));
 
  if(WiFi.status()== WL_CONNECTED){ 
 
    HTTPClient http;   
 
    http.begin(url);  
    int httpResponseCode = http.POST(payload); 
 
    if(httpResponseCode>0){
      String response = http.getString(); 
      Serial.println(httpResponseCode);
      Serial.println(response);
    }else{
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
    }
    http.end();
 
  }else{
 
    Serial.println("Error in WiFi connection");   
 
  }
 
  delay(1000);  //Send a request every second
 
}

void loop() {
}
