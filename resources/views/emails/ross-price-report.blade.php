<h2>ROSS Price Report</h2>

<p>Attached you will find the weekly ROSS price report.</p>

<ul>
    <li>Missing: {{ collect($rows)->where('status', 'Missing')->count() }}</li>
    <li>Price Different: {{ collect($rows)->where('status', 'Price Different')->count() }}</li>
    <li>OK: {{ collect($rows)->where('status', 'OK')->count() }}</li>
</ul>
