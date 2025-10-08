from fastapi import FastAPI
from pydantic import BaseModel
import requests
from bs4 import BeautifulSoup
import html
import json

app = FastAPI()

# URL of your PHP form
PHP_FORM_URL = "http://localhost:8080/form.php"

# Pydantic model for input
class FormData(BaseModel):
    name: str
    email: str
    message: str

@app.post("/protected")
def submit_form(data: FormData):
    # Send POST request to PHP form
    response = requests.post(PHP_FORM_URL, data=data.dict())

    # Parse HTML
    soup = BeautifulSoup(response.text, "html.parser")
    pre_tag = soup.select_one("div.output pre")

    if pre_tag:
        # Unescape HTML entities
        json_str = html.unescape(pre_tag.get_text())

        # Parse JSON
        try:
            api_data = json.loads(json_str)
            return {"success": True, "data": api_data}
        except json.JSONDecodeError:
            return {"success": False, "error": "Failed to parse JSON", "raw": json_str}
    else:
        return {"success": False, "error": "No API response found in PHP form"}
