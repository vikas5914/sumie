import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import axios from 'axios';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Image URL to fetch
const imageUrl = 'https://fmcdn.mangahere.com/store/manga/29422/cover.jpg?token=5c09cd2e0bc3c5f665cbbde14fba86bdf531c7de&ttl=1770336000&v=1755913822';

const fetchImage = async () => {
    try {
        const response = await axios.get(imageUrl, {
            responseType: 'arraybuffer',
            headers: {
                'sec-ch-ua-platform': '"macOS"',
                'Referer': 'https://www.mangahere.cc/',
                'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
                'sec-ch-ua': '"Not(A:Brand";v="8", "Chromium";v="144", "Google Chrome";v="144"',
                'sec-ch-ua-mobile': '?0'
            }
        });
        return response.data;
    } catch (err) {
        throw new Error(err.message);
    }
};

(async () => {
    fs.writeFileSync(path.join(__dirname, 'cover.jpg'), await fetchImage());
})();
