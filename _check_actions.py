from urllib.request import Request, urlopen
import json

url = "https://api.github.com/repos/vitosternberg/plansaludfacil/actions/runs/30822151485/jobs"
req = Request(url, headers={"User-Agent": "codewhale"})
d = json.loads(urlopen(req).read())

for j in d.get('jobs', []):
    print(f"Job: {j['name']} | status: {j['status']} | conclusion: {j['conclusion']}")
    for s in j.get('steps', []):
        print(f"  Step: {s['name']} | status: {s['status']} | conclusion: {s.get('conclusion', '-')}")
