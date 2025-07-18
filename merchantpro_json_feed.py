from flask import Flask, jsonify, request
import base64, requests

app = Flask(__name__)

API_KEY = 'qpu2DCnJpMUy2q4vCi6FLNifg'
API_SECRET = '5ed8df919c46aaaf86fb67c525feb8fd'
SHOP_URL = 'https://opremazanavodnjavanje.rs'

auth = base64.b64encode(f"{API_KEY}:{API_SECRET}".encode()).decode()
HEADERS = {"Authorization": f"Basic {auth}", "Accept": "application/json"}

@app.route('/orderpicking-feed')
def get_orders():
    resp = requests.get(f"{SHOP_URL}/api/v2/orders?include=products", headers=HEADERS)
    data = resp.json().get('data', [])
    out = []
    for o in data:
        fo = {
            "id": o["id"], "order_status": o["status"],
            "order_date": o["created_at"][:10],
            "last_name": o.get("shipping_address", {}).get("last_name", ""),
            "shipping_method": o.get("shipping_method_name","Standard"),
            "order_total": float(o.get("total", 0)),
            "products": []
        }
        for i in o.get("products", []):
            fo["products"].append({
                "product_id": i["product_id"], "sku": i.get("sku",""),
                "name": i.get("name",""), "description": i.get("description",""),
                "price": float(i.get("price",0)), "quantity": int(i.get("quantity",1)),
                "stock_quantity": 0, "image_url": i.get("image",""), "categories": []
            })
        out.append(fo)
    return jsonify(out)

@app.route('/orderpicking-webhook', methods=['POST'])
def webhook():
    data = request.json
    print("Webhook primljen:", data)
    return {"status": "ok"}, 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
