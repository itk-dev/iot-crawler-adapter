# IoT Crawler adapter – LORIOT

## Sending data to the adapter

Before sending data to the adapter, you need an api token. Contact the
maintainer of this repository to get a token.

Data is sent to the adapter by `POST`'ing to the `/loriot` path, e.g.

```sh
curl https://127.0.0.1:8000/loriot?dataFormat=ELSYS \
     --header 'authorization: token «api token»' \
     --header 'content-type: application/json' \
     --data @- <<'JSON'
{
    "data": "010033025c070e1314000f703f",
    "bat": 255,
    "gws": [
        {
            "gweui": "7076FFFFFF010B88",
            "rsig": [
                {
                    "rs2s1": 160,
                    "rfbsb": 99,
                    "ft2d": 863,
                    "foff": 12554,
                    "ftime": -1,
                    "rssisd": 0,
                    "rssis": -92,
                    "etime": "8zjbwTIrGpyQFIAefBxNKg==",
                    "lsnr": 8,
                    "rssic": -91,
                    "chan": 6,
                    "ant": 0
                }
            ],
            "time": "2018-01-09T13:24:56.309729091Z",
            "ts": 1515504296415,
            "snr": 8,
            "rssi": -91
        },
        {
            "gweui": "7076FFFFFF010B32",
            "rsig": [
                {
                    "rs2s1": 80,
                    "rfbsb": 100,
                    "ft2d": 176,
                    "foff": 12425,
                    "ftime": -1,
                    "rssisd": 1,
                    "rssis": -113,
                    "etime": "5G+AW\\/25fLBGfujK6CWV4A==",
                    "lsnr": -8,
                    "rssic": -104,
                    "chan": 6,
                    "ant": 0
                }
            ],
            "time": "2018-01-09T13:24:56.309732793Z",
            "ts": 1515504296442,
            "snr": -8,
            "rssi": -104
        }
    ],
    "ack": false,
    "dr": "SF7 BW125 4\\/5",
    "toa": 61,
    "freq": 868300000,
    "port": 4,
    "fcnt": 6525,
    "ts": 1515504296415,
    "EUI": "ELSYS-A81758FFFE03CFE0",
    "seqno": 17863,
    "cmd": "gw"
}
JSON
```

The value of the `dataFormat` query parameter tells the adapter how to parse the
actual sensor data in the payload (the value of `data`).

## Supported data formats

| Value of `dataFormat` | Details                                   |
|-----------------------|-------------------------------------------|
| `ELSYS`               | https://www.elsys.se/en/elsys-payload/    |
| `0004A30B001E1694`    | Proprietary format used by Aarhus kommune |
| `0004A30B001E307C`    | Proprietary format used by Aarhus kommune |
| `0004A30B001E8EA2`    | Proprietary format used by Aarhus kommune |
