import React, { useEffect, useState } from 'react'
import './Test.css';


function Test() {
    const [count, setCount] = useState(0);

    const add = () => {
        setCount(count + 1);
    }

    return (
        <div className='test'>
            <h1>Test</h1>
            <button className='buttonAdd' onClick={add}>Click me</button>
            <p>{count}</p>
        </div>
    )
}

export default Test
