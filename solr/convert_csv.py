import pandas as pd

df = pd.read_csv("nuovi_da_inserire.csv")

df.to_csv("nuovi_da_inserire_2.csv", sep="|")

