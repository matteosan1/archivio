#!/Users/sani/opt/anaconda3/bin/python

import pandas as pd
import sys, json

with open('out.txt', 'w') as f:
    try:
        filename = sys.argv[1]
        f.write(filename + "\n")
        df = pd.read_csv(filename)
        columns = list(map(str.lower, df.columns))
        
        idx1 = columns.index("nome")
        idx2 = columns.index("comparsa")
        result = {"nomi": df.iloc[:, idx1].tolist(), "ruoli":df.iloc[:, idx2].tolist(),
                  "error":""}
    except Exception as e:
        result = {"error": str(e)}

    f.write(json.dumps(result))
    print (json.dumps(result))
