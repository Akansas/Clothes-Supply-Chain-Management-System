from flask import Flask, request, jsonify
import pandas as pd
from sklearn.cluster import KMeans
from statsmodels.tsa.holtwinters import ExponentialSmoothing

app = Flask(__name__)

@app.route('/segment-customers', methods=['POST'])
def segment_customers():
    data = pd.DataFrame(request.json['customers'])
    if data.empty or 'total_spent' not in data or 'order_count' not in data:
        return jsonify({'error': 'Invalid data'}), 400
    X = data[['total_spent', 'order_count']]
    kmeans = KMeans(n_clusters=3, random_state=42).fit(X)
    data['segment'] = kmeans.labels_
    return jsonify({'segments': data[['id', 'segment']].to_dict(orient='records')})

@app.route('/predict-demand', methods=['POST'])
def predict_demand():
    sales = pd.DataFrame(request.json['sales'])
    if sales.empty or 'date' not in sales or 'amount' not in sales:
        return jsonify({'error': 'Invalid data'}), 400
    sales['date'] = pd.to_datetime(sales['date'])
    ts = sales.groupby('date')['amount'].sum().asfreq('D', fill_value=0)
    if len(ts) < 2:
        return jsonify({'error': 'Not enough data'}), 400
    model = ExponentialSmoothing(ts, trend='add', seasonal=None)
    fit = model.fit()
    forecast = fit.forecast(14)  # next 14 days
    return jsonify({'forecast': {str(k): float(v) for k, v in forecast.items()}})

if __name__ == '__main__':
    app.run(port=5000) 