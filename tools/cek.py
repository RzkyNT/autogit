result = sorted(
    [u for u in githubUsers if u['location'] == 'Indonesia'], 
    key=lambda u: u['followers'], 
    reverse=True
)[:1000]

result = sorted(result, key=lambda u: u['contributions'], reverse=True)[:256]
